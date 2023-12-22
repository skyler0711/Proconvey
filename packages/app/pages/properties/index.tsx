import ProtectedLayout from 'layouts/ProtectedLayout'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import Pagination from '@proconvey/ui/src/components/Pagination'
import { useQuery } from 'urql'
import Skeleton from 'react-loading-skeleton'
import { useEffect, useState } from 'react'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import PropertyCard from 'components/PropertyCard'
import { CrossIcon, PropertyImageIcon } from '@proconvey/ui/src/icons'
import Button from '@proconvey/ui/src/components/Button'
import { graphql } from 'gql'
import { NextSeo } from 'next-seo'

export default function Properties () {
  const [page, setPage] = useState(1)
  const [isVisible, setIsVisible] = useState(true)

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user!,
  }))

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query getClientProperties ($first: Int!, $page: Int!) {
        getClientProperties(first: $first, page: $page) {
          data {
            id
            case_reference
            address {
              id
              line_1
              line_2
              city
              postcode
            }
            type
            conveyancer {
              id
              name
            }
          }
          paginatorInfo {
            total
          }
        }
      }
    `),
    variables: {
      first: 10,
      page: page,
    },
  })

  const handleHideClick = () => {
    setIsVisible(false)
    localStorage.setItem('hidePropertyBanner', 'false')
  }

  useEffect(() => {
    const storageValue = localStorage.getItem('hidePropertyBanner')
    if (storageValue === 'false') {
      setIsVisible(false)
    }
  }, [])


  return (
    <>
      <NextSeo
        title="Properties"
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="md:ml-[1.875rem] md:mr-[3.125rem] md:mt-[3.125rem] m-5">
              {
                isVisible &&
                <div className="bg-primary bg-opacity-10 w-full rounded-xl flex flex-col-reverse md:flex-row justify-between pr-[1.375rem] pt-[1.375rem] pl-[1.875rem] pb-[2.8125rem] gap-5">
                  <div className="flex justify-between max-w-[510px] w-full">
                    <div className="flex flex-col gap-[30px]">
                      <H1>Welcome to <br /> ProConvey, {user?.first_name}!</H1>
                      <p className="text-base leading-7 text-body">
                        Please complete the tasks below for your conveyancer. The system will take you through each task step by step in order to ensure you are sale or purchase ready.
                      </p>
                    </div>
                  </div>

                  <div className="flex flex-col items-center md:items-end">
                    <div className="flex self-end">
                      <Button
                        variant="link"
                        onClick={handleHideClick}
                      >
                        <CrossIcon className="w-3 h-3" />
                      </Button>
                    </div>
                    <PropertyImageIcon className="max-w-[418px] w-full my-auto mx-0" />
                  </div>

                </div>
              }

              <div className="mt-[3.125rem] mb-[1.75rem]">
                <H3>Your Properties</H3>
              </div>

              <div className="flex flex-col gap-[1.25rem]">
                {
                  fetching && !data?.getClientProperties?.data
                    ? <>
                      <Skeleton height={221} />
                      <Skeleton height={221} />
                      <Skeleton height={221} />
                    </>
                    : data?.getClientProperties?.data
                      ? data.getClientProperties.data.map((property) => (
                        <PropertyCard
                          property={property}
                          key={property.id}
                        />
                      ))
                      :
                      <div className="flex items-center justify-center mt-[3.125rem]">
                        <H4>No properties found</H4>
                      </div>
                }
              </div>

              {
                !fetching && (
                  <div className="flex justify-center mt-[1.25rem]">
                    <Pagination
                      total={(data?.getClientProperties?.paginatorInfo?.total ?? 0) / 10}
                      currentPage={page}
                      onPageSelected={setPage}
                    />
                  </div>
                )
              }

            </div>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}
