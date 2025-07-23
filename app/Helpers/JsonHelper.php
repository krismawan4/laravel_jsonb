<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class JsonHelper
{
    /**
     * Remove a key from JSON column
     */
    public static function removeJsonField(string $table, string $jsonColumn, string $fieldName, string $whereColumn, $whereValue): void
    {
        $connection = DB::getDefaultConnection();

        if ($connection === 'pgsql') {
            DB::statement(
                "UPDATE $table SET $jsonColumn = $jsonColumn - ? WHERE $whereColumn = ? AND $jsonColumn ?? ?",
                [$fieldName, $whereValue, $fieldName]
            );
        }

        if ($connection === 'mysql') {
            $jsonPath = '$."' . $fieldName . '"';

            DB::statement(
                "UPDATE $table SET $jsonColumn = JSON_REMOVE($jsonColumn, ?) WHERE $whereColumn = ? AND JSON_CONTAINS_PATH($jsonColumn, 'one', ?)",
                [$jsonPath, $whereValue, $jsonPath]
            );
        }
    }

    /**
     * Add or update a key-value pair in JSON column
     */
    public static function addJsonField(string $table, string $jsonColumn, string $fieldName, $fieldValue, string $whereColumn, $whereValue): void
    {
        $connection = DB::getDefaultConnection();

        if ($connection === 'pgsql') {
            // Use jsonb_set to add/update the field
            DB::statement(
                "UPDATE $table SET $jsonColumn = jsonb_set($jsonColumn, ?, ?) WHERE $whereColumn = ?",
                ['{' . $fieldName . '}', json_encode($fieldValue), $whereValue]
            );
        }

        if ($connection === 'mysql') {
            $jsonPath = '$."' . $fieldName . '"';

            // Use JSON_SET to add/update the field
            DB::statement(
                "UPDATE $table SET $jsonColumn = JSON_SET($jsonColumn, ?, ?) WHERE $whereColumn = ?",
                [$jsonPath, json_encode($fieldValue), $whereValue]
            );
        }
    }

    /**
     * Add or update multiple key-value pairs in JSON column
     */
    public static function addJsonFields(string $table, string $jsonColumn, array $fields, string $whereColumn, $whereValue): void
    {
        $connection = DB::getDefaultConnection();

        if ($connection === 'pgsql') {
            // Build the jsonb_set chain for multiple fields
            $sql = "UPDATE $table SET $jsonColumn = ";
            $params = [];
            $tempColumn = $jsonColumn;

            foreach ($fields as $fieldName => $fieldValue) {
                $sql .= "jsonb_set($tempColumn, ?, ?)";
                $params[] = '{' . $fieldName . '}';
                $params[] = json_encode($fieldValue);
                $tempColumn = "jsonb_set($tempColumn, ?, ?)";

                if (next($fields) !== false) {
                    $sql = str_replace($tempColumn, "jsonb_set($tempColumn, ?, ?)", $sql);
                    $tempColumn = "jsonb_set($tempColumn, ?, ?)";
                }
            }

            $sql .= " WHERE $whereColumn = ?";
            $params[] = $whereValue;

            DB::statement($sql, $params);
        }

        if ($connection === 'mysql') {
            // Build the JSON_SET with multiple paths
            $sql = "UPDATE $table SET $jsonColumn = JSON_SET($jsonColumn";
            $params = [];

            foreach ($fields as $fieldName => $fieldValue) {
                $jsonPath = '$."' . $fieldName . '"';
                $sql .= ", ?, ?";
                $params[] = $jsonPath;
                $params[] = json_encode($fieldValue);
            }

            $sql .= ") WHERE $whereColumn = ?";
            $params[] = $whereValue;

            DB::statement($sql, $params);
        }
    }
}
