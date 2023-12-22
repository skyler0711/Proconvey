import classNames from 'classnames'
import { DoubleChevronIcon } from '../../icons'
import { Children, cloneElement, useState } from 'react'
import SidebarItem from './SidebarItem'
import SidebarSubItem from './SidebarSubItem'

type PropTypes = {
  children: React.ReactNode
}

const ProgressSidebar = ({ children }: PropTypes) => {
  const [collapsed, setCollapsed] = useState<boolean>(false)

  const items = Children.toArray(children)

  return (
    <div className={classNames('relative transition-all h-full overflow-y-auto py-[3.25rem] pt-[3.75rem] px-[23px] w-full border border-primary border-opacity-[14%]', {
      'max-w-[18.375rem]': !collapsed,
      'w-[6.625rem] max-w-[18.375rem] overflow-hidden': collapsed,
    })}>
      <button className={classNames('flex items-center gap-[0.4375rem] absolute top-[18px] bg-secondary hover:bg-secondary-hover active:bg-secondary-active text-primary text-xs leading-[0.875rem] px-2 py-1 focus:outline-none focus-visible:bg-secondary-focus focus-visible:ring-secondary-ring focus-visible:ring-2.5', {
        'rounded-l-md right-0': !collapsed,
        'rounded-r-md left-0': collapsed,
      })}
      onClick={() => setCollapsed(!collapsed)}>
        {collapsed ? 'Show' : 'Hide'}
        {collapsed
          ? <DoubleChevronIcon className="w-2 h-[7px]" />
          : <DoubleChevronIcon className="w-2 h-[7px] transform rotate-180" />}
      </button>
      {
        // @ts-ignore
        Children.map(items, (child, index) => (
          <>
            {
              index !== 0 &&
              <div className="rounded-full relative ml-[1.9375rem] h-[1.875rem] my-1 w-1.5 bg-gainsboro"></div>
            }
            {
              // @ts-ignore
              cloneElement(child, {
                collapsed: collapsed,
              })
            }
          </>
        ))
      }
    </div>
  )
}

ProgressSidebar.Item = SidebarItem
ProgressSidebar.SubItem = SidebarSubItem

export default ProgressSidebar
