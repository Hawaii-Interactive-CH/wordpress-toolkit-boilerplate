import GravityFormsAPI from "@js/gravityForms/GravityFormsAPI";

const FORM_ID = 1;

export class SampleForm {
    static submit(data) {
        return GravityFormsAPI.post(
            `/forms/${FORM_ID}/submissions`,
            {},
            { body: JSON.stringify(data) },
        );
    }
}
