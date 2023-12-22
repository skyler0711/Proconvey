import { graphql } from 'gql'
import { Media } from 'gql/graphql'
import client from 'helpers/client'
import { useCallback, useState } from 'react'
import useDownload from './useDownload'

const useMedia = () => {
  const download = useDownload()

  const [mediaQueryId, setMediaQueryId] = useState<string|undefined>(undefined)
  const [media, setMedia] = useState<Media | undefined>(undefined)
  const [isLoading, setIsLoading] = useState<boolean>(false)

  const mediaQuery = (id: string) => {
    return client.query(
      graphql(`
      query media($id: ID!) {
        media(id: $id) {
          id
          url
          name
          custom_properties
        }
      }
    `),
      {
        id: id,
      },
    ).toPromise()
  }

  const getMedia = useCallback(async (id: string) => {
    setMediaQueryId(id)
    setMedia(undefined)
    setIsLoading(true)

    const response = await mediaQuery(id)

    setMedia(response.data?.media)
    setMediaQueryId(undefined)
    setIsLoading(false)
  }, [])

  const downloadMedia = useCallback(async (id: string) => {
    setMediaQueryId(id)
    setIsLoading(true)

    const response = await mediaQuery(id)

    setMediaQueryId(undefined)
    setIsLoading(false)

    if (response.data?.media) {
      download(response.data?.media.url)
    }
  }, [download])

  return { mediaQueryId, media, getMedia, downloadMedia, isLoading }
}

export default useMedia
