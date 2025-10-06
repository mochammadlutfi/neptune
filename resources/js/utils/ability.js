import { Ability } from "@casl/ability";

// Buat instance ability
export const ability = new Ability([]);

// Fungsi untuk update ability rules
export const updateAbility = (ability, permissions) => {
    const rules = permissions.map((permission) => {
        const [subject, action] = permission.split(".");
        return {
            action,
            subject: subject.charAt(0) + subject.slice(1),
        };
    });
    console.log(rules);
    ability.update(rules);
};
