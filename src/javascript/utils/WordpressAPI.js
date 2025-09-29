const apiUrl = (window).apiUrl;

import { HTTPRequest } from "@utils/HttpRequest";

export default new HTTPRequest(
  `${apiUrl}wp/v2`,
  {},
  {
    cache: "no-cache",
  },
);
