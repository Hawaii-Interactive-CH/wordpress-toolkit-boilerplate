import { HTTPRequest } from "@utils/HTTPRequest";

export default new HTTPRequest(
    `${window.baseUrl}/gravityformsapi`,
    {},
    { cache: "no-cache" },
);
