import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  className?: string
}

const Content = ({ children, className }: PropTypes) => {
  return (
    <div className={classNames('mb-5', className)}>
      {children}
    </div>
  )
}

export default Content
