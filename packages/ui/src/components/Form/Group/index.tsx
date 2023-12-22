import classNames from 'classnames'
import { PropsWithChildren } from 'react'

type GroupProps = PropsWithChildren<{ className?: string }>

const Group = ({ className, children }: GroupProps) => {
  return (
    <div className={classNames('flex flex-col w-full gap-5 md:flex-row', className)}>
      {children}
    </div>
  )
}

export default Group
