import { Menu, Transition } from '@headlessui/react'
import IconButton from '../IconButton'
import { BellIcon } from '../../icons'
import Notification from './Notification'
import { Fragment } from 'react'
import Button from '../Button'
import LoadingSpinner from '../LoadingSpinner'
import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  length: number,
  isLoading?: boolean,
  markAllNotificationsRead: () => void
}

const NotificationsDropdown = ({ children, length = 0, isLoading = false, markAllNotificationsRead }: PropTypes) => {
  return (
    <Menu as="div" className="relative z-10 float-right">
      <Menu.Button as={Fragment}>
        <div className="relative inline-flex">
          <IconButton icon={<BellIcon />} />
          {
            length > 0 &&
            <div className="absolute top-3 right-3 box-border min-h-[0.6875rem] min-w-[0.6875rem] bg-mull rounded-full border-secondary border-[0.125rem]"></div>
          }
        </div>
      </Menu.Button>
      <Transition
        as={Fragment}
        enter="transition ease-out duration-100"
        enterFrom="transform opacity-0 scale-95"
        enterTo="transform opacity-100 scale-100"
        leave="transition ease-in duration-75"
        leaveFrom="transform opacity-100 scale-100"
        leaveTo="transform opacity-0 scale-95"
      >
        <Menu.Items className="absolute sm:right-0 right-[-4.875rem] focus:outline-none w-screen max-w-[600px] bg-white border rounded-[0.625rem] mt-3">
          <div className="flex items-center flex-wrap justify-between border-b px-5 py-[1.4375rem]">
            <div className="flex gap-2.5">
              <p className="font-bold text-[1.375rem] leading-[1.6875rem]">
                Notifications
              </p>
              <div className="flex items-center justify-center rounded-[0.25rem] bg-mull bg-opacity-10 text-mull text-base leading-[1.125rem] py-0.5 px-1.5 min-w-[1.8125rem]">{length}</div>
            </div>
            <Button variant="link" disabled={isLoading} onClick={markAllNotificationsRead}>Mark all as read</Button>
          </div>
          <div className={classNames('overflow-y-auto flex flex-col sm:h-[480px]', {
            'items-center justify-center': isLoading,
          })}>
            {
              isLoading ?
                <LoadingSpinner />
                :
                <>
                  {
                    length > 0 ?
                      children
                      :
                      <div className="flex items-center justify-center h-full">
                        <p>You have no notifications</p>
                      </div>
                  }
                </>
            }
          </div>
        </Menu.Items>
      </Transition>
    </Menu>
  )
}

NotificationsDropdown.Item = Notification

export default NotificationsDropdown
