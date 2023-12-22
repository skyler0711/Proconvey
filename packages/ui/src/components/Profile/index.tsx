import { Children, cloneElement, Fragment, isValidElement, MouseEventHandler, ReactNode } from 'react'
import { Menu, Transition } from '@headlessui/react'
import { ChevronDownIcon } from '../../icons'
import classNames from 'classnames'

type PropTypes = {
  user: {
    first_name: string
    last_name: string
    profile_image?: { url?: string } | null
  }
  children: ReactNode
}

type ProfileItemTypes = {
  children: ReactNode
  active?: boolean
  onClick?: MouseEventHandler<HTMLDivElement>
}

const ProfileItem = ({ children, active, onClick }: ProfileItemTypes) => {
  return (
    <div
      onClick={onClick}
      className={classNames('border border-input-active border-t-input-ring border-b-input-ring first:border-t-input-active last:border-b-input-active px-3 py-4 first:rounded-t-lg last:rounded-b-lg hover:bg-primary/10', {
        'bg-secondary-active': active,
        'bg-white': !active,
      })}
    >
      {children}
    </div>
  )
}

const Profile = ({ user, children }: PropTypes) => {
  return (
    <Menu>
      {({ open }) => (
        <div className="relative max-w-[320px]">
          <Menu.Button className="group focus:outline-none">
            <div className="z-10 flex items-center gap-2">
              <div className="flex items-center gap-3">
                {
                  user?.profile_image?.url
                    ?
                    <img className={`rounded-[10px] w-[50px] h-[50px] group-focus-visible:ring-2.5 group-focus-visible:ring-select-ring ${open ? 'ring-2.5 ring-select-ring' : ''}`} src={user?.profile_image?.url} alt="Profile" />
                    :
                    <div className={`flex items-center justify-center bg-primary text-white rounded-[10px] w-[50px] h-[50px] group-focus-visible:ring-2.5 group-focus-visible:ring-select-ring ${open ? 'ring-2.5 ring-select-ring' : ''}`}>
                      <p className="leading-[1rem]">{user?.first_name.charAt(0)}</p>
                    </div>
                }
                <p className="hidden text-base font-medium lg:block">{user?.first_name} {user?.last_name}</p>
              </div>
              <ChevronDownIcon className={`transition h-5 w-5 hidden lg:block text-primary ${open ? 'rotate-180' : ''}`} />
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
            <Menu.Items className="absolute right-0 z-10 flex flex-col w-auto mt-3 overflow-auto text-base bg-white rounded-lg cursor-pointer lg:w-full focus:outline-none">
              {
                children && Children.map(children, (child) => {
                  return (
                    <Menu.Item key={'item-'}>
                      {
                        ({ active }) => {
                          if (isValidElement(child)) {
                            return cloneElement(child, {
                              active: active,
                            } as Partial<unknown>)
                          }
                          return <></>
                        }
                      }
                    </Menu.Item>
                  )
                })
              }
            </Menu.Items>
          </Transition>

        </div>
      )}
    </Menu>
  )
}

Profile.Item = ProfileItem

export default Profile
