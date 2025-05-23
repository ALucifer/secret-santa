export function useCookie() {
    function readCookie(name: string) {
        const cookies = document.cookie.split('; ')
        const value = cookies
            .find(c => c.startsWith(name + "="))
            ?.split('=')[1]
        if (value === undefined) {
            return null
        }
        return decodeURIComponent(value)
    }

    return { readCookie }
}