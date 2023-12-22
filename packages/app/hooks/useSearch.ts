import { graphql } from 'gql'
import { SearchResult, User } from 'gql/graphql'
import { useEffect, useState } from 'react'
import { useQuery } from 'urql'

export type Result = {
  text: string | null | undefined
  href: string | null | undefined
  type: string | null | undefined
  users: User[]
}

export type ResultsType = {
  display_text?: SearchResult['display_text']
  id?: SearchResult['id']
  line_1?: SearchResult['line_1']
  type?: SearchResult['type']
  users: {
    id: User['id']
    first_name?: User['first_name']
    last_name?: User['last_name']
  }[]
}

const useSearch = () => {
  const [search, setSearch] = useState('')
  const [results, setResults] = useState<ResultsType[]>([])
  const [noResults, setNoResults] = useState(false)

  const [{ fetching: fetchingGlobalSearch, data: globalSearchData }] = useQuery({
    query: graphql(`
      query doGlobalQuery ($filters: GlobalSearchInput!) {
        globalSearch(input: $filters) {
          id
          type
          display_text
          line_1
          users {
            id
            first_name
            last_name
          }
        }
      }
    `),
    variables: {
      filters: {
        search: search,
      },
    },
    pause: !search,
  })

  useEffect(() => {
    if (globalSearchData && globalSearchData.globalSearch && globalSearchData.globalSearch.length > 0) {
      setNoResults(false)
      setResults(globalSearchData.globalSearch)
    } else {
      setNoResults(true)
    }
  }, [globalSearchData])


  return { setSearch, fetchingGlobalSearch, results, noResults }
}

export default useSearch
