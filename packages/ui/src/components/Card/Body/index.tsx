import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  padContent?: boolean
}

const Body = ({ children, padContent = true }: PropTypes) => {
  return (
    <div className={classNames({
      'p-5': padContent,
      'p-0': !padContent,
    },
    )}>
      {children}
    </div>
  )
}

export default Body
