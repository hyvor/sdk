import {getSchemas} from "./helpers.ts";

export function generatePhp() {
    const schemas = getSchemas();
    console.log(schemas);
}