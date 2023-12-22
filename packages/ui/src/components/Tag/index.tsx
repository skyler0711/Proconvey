import classNames from 'classnames'

type PropTypes = {
  variant?: 'danger' | 'success' | 'warning'
  children: React.ReactNode
  className?: string
}

const Tag = ({ variant, className, children }: PropTypes) => {
  const innerClassName = classNames('bg-opacity-10 rounded-md px-3 py-1 inline-flex items-center gap-3 text-sm', {
    'bg-body border-body': !variant,
    'text-danger bg-danger border-danger': variant === 'danger',
    'text-mint bg-mint/10 border-mint': variant === 'success',
    'text-peach bg-peach/10 border-peach': variant === 'warning',
  }, className)

  return (
    <div className={innerClassName}>
      {children}
    </div>
  )
}

export default Tag
