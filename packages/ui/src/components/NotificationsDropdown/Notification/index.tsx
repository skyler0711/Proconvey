import { Menu } from '@headlessui/react'
import classNames from 'classnames'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { useCallback, useEffect, useState } from 'react'
import { AlertIcon, ChevronDownIcon } from '../../../icons'
import Button from '../../Button'
import Link from 'next/link'

dayjs.extend(relativeTime)

type PropTypes = {
  notificationData?: {
    type?: string | null
  }
  children: React.ReactNode,
  to?: string,
  timestamp: string
  icon?: 'alert'
}

const renderIcon = (alert: string) => {
  switch (alert) {
    case 'alert':
      return <AlertIcon />
    default:
      return null
  }
}

const Notification = ({ children, notificationData, timestamp, icon = 'alert' }: PropTypes) => {
  const [activeUrl, setActiveUrl] = useState<undefined | string>(undefined)

  const setUrl = useCallback(() => () => {
    let url = undefined

    switch (notificationData?.type) {
      case 'profile_settings':
        url = '/settings/profile'
        break
      default:
        break
    }

    setActiveUrl(url)
  }, [notificationData?.type])

  useEffect(() => {
    if (notificationData) {
      setUrl()
    }
  }, [notificationData, setUrl])

  return (
    <Menu.Item>
      {({ active, close }) => (
        <div className={classNames('hover:bg-secondary flex items-center gap-3 min-h-[82px] border-b last:border-none p-5', {
          'bg-secondary': active,
        })} onClick={close}>
          <div className="flex items-center justify-center bg-alert min-w-[2.5rem] min-h-[2.5rem] rounded-[0.625rem]">
            {renderIcon(icon)}
          </div>

          <div>
            <p className="font-medium text-sm leading-[1.3125rem]">{children}</p>
            <p className="text-xs leading-4 text-body text-opacity-60">{dayjs(timestamp).fromNow()}</p>
          </div>

          <div className="hidden ml-auto sm:block">
            {
              (notificationData?.type && activeUrl) &&
              <Link href={activeUrl}>
                <Button variant="link" onClick={close}>
                  <div className="flex items-center gap-1">
                    <span>View</span>
                    <ChevronDownIcon className="w-3 h-3 -rotate-90" />
                  </div>
                </Button>
              </Link>
            }
          </div>
        </div>
      )}
    </Menu.Item>

  )
}

export default Notification
