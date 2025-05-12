import {ref} from "vue";
import {useCookie} from "@app/composables/useCookie";

interface Options {
    method: string,
    headers: {
        'Authorization'?: string,
        'Content-Type': string,
    },
    body?: string,
}

const defaultOptions: Options = {
    method: 'GET',
    headers: {
        "Content-Type": "application/json",
    },
}

export async function useFetch<T>(url: string, options?: Options) {
    const data = ref<T | null>(null)
    const error = ref<boolean>(false)

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

    try {
        const response = await fetch(url, mergedOptions)

        data.value = await response.json()
    } catch {
        error.value = true
    }

    return {
        data,
        error
    }
}