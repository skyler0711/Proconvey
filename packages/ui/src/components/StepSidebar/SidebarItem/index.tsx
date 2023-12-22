import classNames from 'classnames'
import { CheckmarkIcon } from '../../../icons'

type PropTypes = {
  step?: number,
  active?: boolean
  completed?: boolean,
  children: React.ReactNode
}

const SidebarItem = ({ step, active, completed, children }: PropTypes) => {
  return (
    <div className="flex items-center gap-3.5">
      <div className={classNames('flex items-center justify-center relative', {
        'min-h-[2.625rem] min-w-[2.625rem] -ml-[0.375rem]': active,
        'min-h-[1.875rem] min-w-[1.875rem]': !active,
      })}>
        <div className={classNames('flex min-h-[1.875rem] min-w-[1.875rem] items-center justify-center rounded-full border border-opacity-[15%] font-bold text-sm leading-[1rem]', {
          'ring-offset-[4px] ring-4 ring-chalk text-primary bg-primary bg-opacity-10 border-primary': active,
          'text-body border-body': !active,
          'bg-primary': completed,
          'bg-body bg-opacity-10': !completed,
        })}>
          {
            completed
              ? <CheckmarkIcon className="w-[0.8125rem] h-[0.625rem] text-white" />
              : step
          }
        </div>
      </div>
      <p className={classNames('font-bold text-base leading-[1.1875rem]', {
        'text-primary': active,
        'text-body': !active,
      })}>{children}</p>
    </div>
  )
}

export default SidebarItem
