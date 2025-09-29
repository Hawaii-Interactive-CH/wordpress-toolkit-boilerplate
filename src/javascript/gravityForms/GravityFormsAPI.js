import { HTTPRequest } from "@utils/HTTPRequest";

export default new HTTPRequest(
    `${toolkit.base_url}/gravityformsapi`,
    {},
    { cache: "no-cache" },
);
