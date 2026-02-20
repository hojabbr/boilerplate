import type { ComponentPropsWithoutRef } from 'react';
import {
    Field,
    FieldContent,
    FieldError,
    FieldLabel,
} from '@/components/ui/field';
import { Input } from '@/components/ui/input';

type InputProps = ComponentPropsWithoutRef<typeof Input>;

interface LabeledInputFieldProps extends Omit<InputProps, 'id'> {
    id: string;
    label: string;
    error?: string;
}

export function LabeledInputField({
    id,
    label,
    error,
    className,
    ...inputProps
}: LabeledInputFieldProps) {
    return (
        <Field className="grid gap-2">
            <FieldLabel htmlFor={id}>{label}</FieldLabel>
            <FieldContent>
                <Input id={id} className={className} {...inputProps} />
                <FieldError>{error}</FieldError>
            </FieldContent>
        </Field>
    );
}
