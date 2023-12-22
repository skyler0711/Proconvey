import { Combobox, Transition } from '@headlessui/react'
import classNames from 'classnames'
import { SearchIcon } from '../../icons'
import { useState, useEffect, Fragment } from 'react'
import { useDebounce } from 'use-debounce'
import Form from '../Form'
import Link from 'next/link'
import { ResultsType } from '@proconvey/app/hooks/useSearch'


export type User = {
  id: number
  first_name: string
  last_name: string
}

export type Result = {
  id: string | null | undefined
  text: string | null | undefined
  href: string | null | undefined
  type: string | null | undefined
  users: User[]
}

type PropTypes = {
  results: ResultsType[] | undefined
  onChange?: (arg0: string) => void
  noResults: boolean
  fetching: boolean
  className?: string
  onClick?: () => void
}

const Searchbar = ({
  results = [],
  onChange = () => { },
  noResults,
  fetching,
  className,
  onClick = () => { },
}: PropTypes) => {

  const [_, setSelectedResult] = useState<Result | undefined>(undefined)

  const [searchQuery, setSearchQuery] = useState<string>('')
  const [debouncedSearch] = useDebounce(searchQuery, 500)

  useEffect(() => {
    onChange(debouncedSearch)
  }, [debouncedSearch, onChange])


  return (
    <div className={`relative flex ${className}`}>
      <Combobox onChange={setSelectedResult}>
        <Combobox.Input
          as={Fragment}
          displayValue={(result: Result) => (result.text ?? '')}
          onChange={(event) => setSearchQuery(event.target.value)}
        >
          <Form.Input
            placeholder="Search by client’s name, address or postcode"
            prefixIcon={<SearchIcon className="w-5 h-5 text-body text-opacity-60" />}
            value={searchQuery}
          />
        </Combobox.Input>

        <Combobox.Options className="absolute z-10 w-full top-full focus:outline-none">
          {noResults && !fetching && debouncedSearch.length > 0 ?
            <Transition
              show={true}
              className="bg-white border rounded-[0.625rem] mt-3"
              enter="transition-all ease-out duration-100"
              enterFrom="transform opacity-0 scale-95"
              enterTo="transform opacity-100 scale-100"
              leave="transition-all ease-in duration-75"
              leaveFrom="transform opacity-100 scale-100"
              leaveTo="transform opacity-0 scale-95"
            >

              <div className="flex items-center gap-3 min-h-[60px] border-b first:rounded-t-[0.625rem] last:border-none p-5">No results found</div>

            </Transition>
            :
            <Transition
              show={results ? results.length > 0 && searchQuery !== '' : false}
              className="cursor-pointer bg-white border rounded-[0.625rem] mt-3"
              enter="transition-all ease-out duration-100"
              enterFrom="transform opacity-0 scale-95"
              enterTo="transform opacity-100 scale-100"
              leave="transition-all ease-in duration-75"
              leaveFrom="transform opacity-100 scale-100"
              leaveTo="transform opacity-0 scale-95"
            >
              <div className="transition-all overflow-y-auto flex flex-col h-min max-h-[480px]">
                {
                  results && results.map((result, key) => (
                    <Combobox.Option
                      as={Link}
                      key={`searchbar-item-${key}`}
                      href={`/clients/${result.id}` ?? ''}
                      value={result}
                      className={({ active }) =>
                        classNames('hover:bg-secondary flex items-center gap-3 min-h-[60px] box-content border-b first:rounded-t-[0.625rem] last:border-none p-5', {
                          'bg-secondary': active,
                        })
                      }
                      onClick={onClick}
                    >
                      <div
                        className="font-medium w-full text-sm leading-[1.3125rem] flex flex-col">
                        <span className="mb-1 text-xs text-body opacity-60">{result.type}</span>
                        {result.display_text}
                        <span className="mt-1 text-xs text-body opacity-60">
                          {result.users.map(user => `${user.first_name} ${user.last_name}`).join(', ')}
                        </span>
                      </div>
                    </Combobox.Option>
                  ))
                }
              </div>
            </Transition>
          }


        </Combobox.Options>
      </Combobox>
    </div>
  )
}

export default Searchbar
