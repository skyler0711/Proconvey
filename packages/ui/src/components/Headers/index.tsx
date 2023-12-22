import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  className?: string
}

const H1 = ({ children, className }: PropTypes) => {
  return (
    <h1 className={classNames('text-[2.5rem] font-bold text-body leading-[3.125rem]', className)}>
      {children}
    </h1>
  )
}

const H2 = ({ children, className }: PropTypes) => {
  return (
    <h2 className={classNames('text-[2rem] font-bold text-body leading-[2.875rem]', className)}>
      {children}
    </h2>
  )
}

const H3 = ({ children, className }: PropTypes) => {
  return (
    <h3 className={classNames('text-[1.375rem] font-bold text-body', className)}>
      {children}
    </h3>
  )
}

const H4 = ({ children, className }: PropTypes) => {
  return (
    <h4 className={classNames('text-[1.25rem] font-bold text-body leading-[1.6875rem]', className)}>
      {children}
    </h4>
  )
}

export {
  H1,
  H2,
  H3,
  H4,
}
