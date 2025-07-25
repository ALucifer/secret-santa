import {ref} from "vue";
import {useCookie} from "@app/composables/useCookie";

interface Violation {
    property: string,
    message: string,
}

interface Violations {
    violations: Violation[]
}

export interface Options {
    method: string,
    headers: {
        'Authorization'?: string,
        'Content-Type': string,
        'Accept': string,
    },
    body?: string,
}

const defaultOptions: Options = {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
}

export async function useFetch<T>(url: string, options?: Options) {
    const data = ref<T | null>(null)
    const hasError = ref<boolean>(false)
    const errors = ref<Violations|null>(null)

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

    const response = await fetch(url, mergedOptions)

    const content = await response.json()

    if (!response.ok) {
        hasError.value = true
    }

    (hasError.value) ? errors.value = content : data.value = content

    return {
        data,
        hasError,
        errors
    }
}