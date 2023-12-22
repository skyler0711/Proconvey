import { Children, cloneElement, Fragment } from 'react'
import { Disclosure, Transition } from '@headlessui/react'
import classNames from 'classnames'
import { ChevronDownIcon } from '../../../icons'
import PercentageCircle from '../../PercentageCircle'

type PropTypes = {
  collapsed: boolean
  children: React.ReactNode
  completed: boolean
  progress: number
  text: string
  active: boolean
}

const SidebarItem = ({ collapsed, children, progress, text, active = false }: PropTypes) => {
  const items = Children.toArray(children)

  return (
    <Disclosure>
      {({ open }) => (
        <>
          <Disclosure.Button className="relative flex items-center w-full gap-3 group focus:outline-none">
            <div className="min-w-[4.125rem]">
              <div className="mx-auto max-w-min">
                <PercentageCircle strokeWidth={active ? 6 : 4} percentage={progress} height={active ? 66 : 48} width={active ? 66 : 48} fontSize={active ? 12 : 9} />
              </div>
            </div>
            <Transition
              className="flex items-center justify-between w-full"
              show={!collapsed}
              enter="transition-opacity duration-75"
              enterFrom="opacity-0"
              enterTo="opacity-100"
              leave="transition-opacity duration-75"
              leaveFrom="opacity-100"
              leaveTo="opacity-0"
            >
              <p className="text-body text-base text-left leading-[1.125rem] font-medium group-focus:underline">{text}</p>
              <ChevronDownIcon className={classNames('transition flex-shrink-0 w-[1rem] ml-[0.5rem]', { 'rotate-180': open })} />
            </Transition>
          </Disclosure.Button>
          <Disclosure.Panel className="pl-[1.4375rem]">
            <Transition
              show={!collapsed}
              enter="transition-opacity duration-75"
              enterFrom="opacity-0"
              enterTo="opacity-100"
              leave="transition-opacity duration-75"
              leaveFrom="opacity-100"
              leaveTo="opacity-0"
            >
              {
                // @ts-ignore
                Children.map(items, (child, index) => (
                  <>
                    {
                      // @ts-ignore
                      cloneElement(child, {
                        index: index,
                        lastChild: (index + 1) === items.length,
                      })
                    }
                  </>
                ))
              }
            </Transition>
          </Disclosure.Panel>
        </>
      )}
    </Disclosure>
  )
}

export default SidebarItem
