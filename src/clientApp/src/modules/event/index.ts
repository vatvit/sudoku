export const buildEventSource = (url: URL | string) => {
    return new EventSource(url, {});
};