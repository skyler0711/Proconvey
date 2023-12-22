import classNames from 'classnames'

type PropTypes = {
  type: 'Sale' | 'Purchase' | 'Remortgage'
    | 'Owner' | 'Buyer' | 'Remortgager'
    | 'Attorney' | 'Deputy' | 'Executor'  | 'Giftor'
  children: React.ReactNode
  className?: string
}

const PropertyTag = ({ type, className, children }: PropTypes) => {
  const innerClassName = classNames('bg-opacity-10 rounded-md px-3 py-1 inline-flex items-center gap-3 text-base font-medium capitalize', {
    'bg-body border-body': !type,
    'text-mull bg-mull': type === 'Purchase' || type === 'Owner',
    'text-crystal-blue bg-crystal-blue': type === 'Sale' || type === 'Buyer',
    'text-oceano bg-oceano': type === 'Remortgage' || type === 'Remortgager',
    'text-navy bg-navy': type === 'Attorney' || type === 'Deputy',
    'text-peach bg-peach': type === 'Executor',
    'text-primary bg-primary': type === 'Giftor',
  }, className)

  return (
    <div className={innerClassName}>
      {children}
    </div>
  )
}

export default PropertyTag
