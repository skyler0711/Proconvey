import { Disclosure, Transition } from '@headlessui/react'
import { Fragment } from 'react'

type PropTypes = {
  text: string
  children: React.ReactNode
  defaultOpen: boolean
  icon: React.ReactNode
}

const MenuDropdown = ({ text, children, defaultOpen, icon }: PropTypes) => {
  return (
    <Disclosure defaultOpen={defaultOpen}>
      {({ open }) => (
        <div>
          <Disclosure.Button className="flex items-center gap-6 px-[1.125rem] py-[0.625rem] focus:outline-none focus-visible:bg-primary focus-visible:bg-opacity-10 focus-visible:text-primary rounded-[0.625rem]">
            <div className={`text-base ${open ? 'text-primary' : 'text-black'}`}>{icon}</div>
            <p className={`text-base leading-[1.1875rem] font-medium ${open ? 'text-primary' : 'text-black'}`}>{text}</p>
          </Disclosure.Button>
          <Transition
            as={Fragment}
            enter="transition ease-out duration-100"
            enterFrom="transform opacity-0 scale-95"
            enterTo="transform opacity-100 scale-100"
            leave="transition ease-in duration-75"
            leaveFrom="transform opacity-100 scale-100"
            leaveTo="transform opacity-0 scale-95"
          >
            <Disclosure.Panel className="flex flex-col gap-5 border-l ml-[1.8125rem] pl-2.5 py-2.5">
              {children}
            </Disclosure.Panel>
          </Transition>
        </div>
      )}
    </Disclosure>
  )
}

export default MenuDropdown
