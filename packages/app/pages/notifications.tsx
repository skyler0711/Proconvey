import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H3 } from '@proconvey/ui/src/components/Headers'
import Table from '@proconvey/ui/src/components/Table'
import { AlertIcon, ChevronDownIcon } from '@proconvey/ui/src/icons'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import Link from 'next/link'
import React from 'react'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { NextSeo } from 'next-seo'
dayjs.extend(relativeTime)

const Notifications = () => {

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user!,
  }))


  const renderIcon = (alert: string) => {
    switch (alert) {
      case 'alert':
        return <AlertIcon />
      default:
        return null
    }
  }

  return (
    <>
      <NextSeo
        title="Notifications"
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="ml-[1.875rem] mr-[3.125rem] mt-[3.125rem]">
              <Card>
                <Card.Header>
                  <div className="flex">
                    <H3>Notifications</H3>
                    {
                      user?.unread_notifications &&
                      <div className="flex items-center justify-center rounded-[0.25rem] bg-mull bg-opacity-10 text-mull text-base leading-[1.125rem] py-0.5 px-1.5 min-w-[1.8125rem] ml-[0.625rem]">
                        {user.unread_notifications?.length}
                      </div>
                    }

                  </div>

                </Card.Header>

                <hr />

                <Table>
                  <Table.Body>
                    {
                      user?.unread_notifications?.length
                        ? (
                          user.unread_notifications.map((notification, index) => {
                            return (
                              <Table.Row key={index}>
                                <Table.Cell>
                                  <div className="flex flex-col items-center justify-between sm:flex-row">
                                    <div className="flex self-start">
                                      <div className="flex items-center justify-center bg-alert min-w-[2.5rem] w-4 min-h-[2.5rem] rounded-[0.625rem] mr-[0.75rem]">
                                        {renderIcon('alert')}
                                      </div>

                                      <div className="flex flex-col">
                                        <p className="font-medium text-sm leading-[1.3125rem]">{notification?.data?.message}</p>
                                        <p className="text-xs leading-4 text-body text-opacity-60">{dayjs(notification?.created_at).fromNow()}</p>
                                      </div>
                                    </div>

                                    {
                                      //this is for when notifications may have an active link
                                      // (notification?.type && activeUrl) &&
                                      (notification?.type) &&
                                      <div className="self-end">
                                        <Link href="#">
                                          <Button variant="link">
                                            <div className="flex items-center gap-1">
                                              <span>View</span>
                                              <ChevronDownIcon className="w-3 h-3 -rotate-90" />
                                            </div>
                                          </Button>
                                        </Link>
                                      </div>
                                    }

                                  </div>
                                </Table.Cell>
                              </Table.Row>
                            )
                          })
                        )
                        : <div className="p-5">You have no unread notifications</div>
                    }

                  </Table.Body>
                </Table>

              </Card>

            </div>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

export default Notifications
