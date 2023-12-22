import classNames from 'classnames'
import { WarningIcon } from '../../icons'

type PropTypes = {
  variant?: 'danger' | 'success'
  children: React.ReactNode
  className?: string
}

const Alert = ({ variant, children, className }: PropTypes) => {
  const innerClassName = classNames('flex items-center text-sm p-[6px] rounded-lg bg-opacity-5 border', {
    'w-full': true,

    'bg-body border-body': !variant,
    'text-danger bg-danger border-danger': variant === 'danger',
    'text-mint bg-mint/10 border-mint': variant === 'success',
  }, className)

  return (
    <div className={innerClassName}>
      {
        variant === 'danger' &&
        <WarningIcon className="w-5 h-5 mr-2" />
      }
      {children}
    </div>
  )
}

export default Alert
