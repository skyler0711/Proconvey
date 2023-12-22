import { ClientOptions, createClient } from '@urql/core'
import Cookies from 'js-cookie'

const fetchApiCookie = async () => {
  await fetch(`${process.env.NEXT_PUBLIC_API_ENDPOINT}/sanctum/csrf-cookie`, {
    credentials: 'include',
  })

  return Cookies.get('XSRF-TOKEN')
}

export const clientOptions: ClientOptions = {
  url: process.env.NEXT_PUBLIC_GRAPHQL_ENDPOINT!,
  requestPolicy: 'network-only',
  fetch: async (input, init) => {
    const token = Cookies.get('XSRF-TOKEN') ?? await fetchApiCookie()

    init!.credentials = 'include'

    init!.headers = new Headers(init!.headers)

    if (token) {
      init!.headers.set('X-XSRF-TOKEN', token)
    }

    return fetch(input, init)
  },
}

export default createClient(clientOptions)
