import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import Searchbar from '@proconvey/ui/src/components/Searchbar'
import Select from '@proconvey/ui/src/components/Form/Select'
import Pagination from '@proconvey/ui/src/components/Pagination'
import { useQuery } from 'urql'
import { UserStatus } from 'types/enums/UserStatus'
import { graphql } from 'gql'
import Skeleton from 'react-loading-skeleton'
import { useState } from 'react'
import PropertyUserCard from 'components/PropertyUserCard'
import { NextSeo } from 'next-seo'

export default function Client () {
  const [searchTerm, setSearchTerm] = useState('')
  const [page, setPage] = useState(1)
  const [filterOption, setFilterOption] = useState('all')


  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query properties ($first: Int!, $page: Int!, $filters: PropertyFilterInputs) {
        properties(first: $first, page: $page, filters: $filters) {
          data {
            id
            type
            archived_at
            users {
              id
              title
              first_name
              last_name
              email
              role
              job_role
              job_bio
              suffix
              phone
            }
            address {
              id
              line_1
              line_2
              city
              postcode
            }
          }
          paginatorInfo {
            total
            lastPage
          }
        }
      }
    `),
    variables: {
      first: 10,
      page: page,
      filters: {
        search: searchTerm || '',
        filter_option: filterOption,
      },
    },
  })


  return (
    <>
      <NextSeo
        title="Clients"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="flex flex-col justify-between gap-x-5 lg:flex-row">
              <div className="flex lg:items-center gap-[0.5625rem] mb-[1.875rem]">
                <H1>Clients</H1>
                <p className="mt-3 text-body opacity-60">
                  {
                    data?.properties?.paginatorInfo?.total
                      ? `(${data?.properties?.paginatorInfo?.total})`
                      : null
                  }
                </p>
              </div>
              <div className="flex flex-col justify-end w-full mb-2 lg:flex-row gap-y-2 gap-x-5">
                <div className="flex-1 lg:max-w-[450px]">
                  <Searchbar
                    onChange={setSearchTerm}
                    results={undefined}
                    noResults={false}
                    fetching={false}
                  />
                </div>

                <Select
                  className="lg:max-w-[250px]"
                  defaultValue={{ text: 'All Clients', value: 'all' }}
                  options={(Object.keys(UserStatus) as Array<keyof typeof UserStatus>)
                    .map(k => ({ text: k, value: UserStatus[k] }))}
                  onChange={e => setFilterOption(e.value)}
                />
              </div>

            </div>
            {
              !fetching && data?.properties?.data?.length === 0 && (
                <div className="flex items-center justify-center mt-[3.125rem]">
                  <H3>No clients found</H3>
                </div>
              )
            }

            <div className="flex flex-col gap-[1.25rem]">
              {
                fetching && !data?.properties?.data
                  ? <>
                    <Skeleton height={221} />
                    <Skeleton height={221} />
                    <Skeleton height={221} />
                  </>
                  : data?.properties?.data?.map((property) => (
                    <PropertyUserCard
                      property={property}
                      key={property.id}
                    />
                  ))
              }
            </div>

            {
              !fetching &&
              (
                <div className="flex justify-center mt-[1.25rem]">
                  {
                    data?.properties?.paginatorInfo?.total && data?.properties?.paginatorInfo.total >= 10 ?
                      <Pagination
                        total={data?.properties?.paginatorInfo?.lastPage}
                        currentPage={page}
                        onPageSelected={(page) => setPage(page)}
                      />
                      : null
                  }
                </div>
              )
            }
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
