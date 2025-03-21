import {ref} from "vue";
import {useCookie} from "@app/composables/useCookie";

interface Options {
    method: string,
    headers: {
        'Authorization'?: string,
        'Content-Type': string,
    }
}

const defaultOptions: Options = {
    method: 'GET',
    headers: {
        "Content-Type": "application/json",
    }
}

export function useFetch<T>(url: string, options?: Options) {
    const data = ref<T | null>(null)

    const { readCookie } = useCookie()

    const mergedOptions: Options = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options?.headers,
            'Authorization': 'Bearer ' + readCookie('AUTH_TOKEN'),
        }
    };

    fetch(url, mergedOptions)
        .then((response) => response.json())
        .then((items) => data.value = items)

    return {
        data
    }
}