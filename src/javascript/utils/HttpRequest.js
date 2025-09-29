export class HTTPRequest {
    constructor(apiURL, defaultURLParams, defaultHeaders) {
        this.apiURL = apiURL;
        this.defaultURLParams = defaultURLParams;
        this.defaultHeaders = defaultHeaders;
    }

    request(method, route, urlParams = {}, headers = {}) {
        headers = Object.assign(headers, this.defaultHeaders, {
            method: method,
        });
        urlParams = Object.assign(urlParams, this.defaultURLParams);

        return fetch(
            `${this.apiURL}${route}?${new URLSearchParams(urlParams)}`,
            headers,
        ).then((response) => {
            let json = response.json();
            if (response.status >= 200 && response.status < 300) {
                return json.then((json) => ({
                    headers: response.headers,
                    status: response.status,
                    json,
                }));
            }
            return json.then(Promise.reject.bind(Promise));
        });
    }

    get(route, urlParams = {}, headers = {}) {
        return this.request("GET", route, urlParams, headers);
    }

    post(route, urlParams = {}, headers = {}) {
        return this.request("POST", route, urlParams, headers);
    }

    put(route, urlParams = {}, headers = {}) {
        return this.request("PUT", route, urlParams, headers);
    }

    delete(route, urlParams = {}, headers = {}) {
        return this.request("DELETE", route, urlParams, headers);
    }
}
