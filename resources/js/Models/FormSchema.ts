export interface ComponentProp {
    key: string;
    value: string | number | boolean | null;
    validation_message: string;
}

export interface FieldObject {
    type: string;
    label: string;
    order: number;
    field_name: string;
    is_visible: boolean;
    is_editable: boolean;
    default_value: string | number | null;
    component_props: ComponentProp[];
}

export interface FormSchema {
    name: string;
    data: FieldObject[];
}
