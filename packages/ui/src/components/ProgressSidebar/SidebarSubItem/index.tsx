import classNames from 'classnames'
import { CheckmarkIcon } from '../../../icons'

type PropTypes = {
  index: number,
  lastChild: boolean,
  children: React.ReactNode
  active: boolean
  completed: boolean
  collapsed: boolean
  key?: string | number
}

type CheckmarkPropTypes = {
  active: boolean
  completed: boolean
}

const Checkmark = ({ active = false, completed = false }: CheckmarkPropTypes) => {
  const circleClassName = classNames('z-10 rounded-full min-w-[1.375rem] min-h-[1.375rem] flex justify-center items-center border border-[0.125rem] ', {
    'border-gainsboro bg-white': !completed,
    'border-primary': active,
    'bg-primary border-primary': completed,
  })

  const checkMarkClassName = classNames('text-gainsboro', {
    'text-white': completed,
  })

  return (
    <div className={circleClassName}>
      <CheckmarkIcon className={checkMarkClassName} />
    </div>
  )
}

const SidebarSubItem = ({ index, lastChild = false, children, active = false, completed = false }: PropTypes) => {
  return (
    <div className="flex gap-[1.9375rem] h-[calc(100%+20px)]">
      <div className="relative flex flex-col items-center">
        {
          index === 0 &&
          <div className="relative -mb-1 mt-1 w-1.5 bg-gainsboro rounded-t-full h-[1.5rem]"></div>
        }
        <Checkmark active={active} completed={completed} />
        {
          !lastChild &&
          <div className="relative -mt-1 w-1.5 bg-gainsboro h-[1.5rem]"></div>
        }
      </div>
      <div className={classNames({
        'mt-[23px]': index === 0,
        'pb-3': !lastChild,
      })}>
        {children}
      </div>
    </div>
  )
}

export default SidebarSubItem
