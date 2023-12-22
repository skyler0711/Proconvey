import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  active?: boolean
  icon?: React.ReactNode
}

const MenuItem = ({ children, active = false, icon }: PropTypes) => {
  return (
    <span className={classNames('flex items-center gap-6 text-base leading-[1.1875rem] font-medium px-[18px] py-2.5 focus:outline-none focus-visible:bg-primary/10 focus-visible:text-primary rounded-[0.625rem]', {
      'text-primary bg-primary/10': active,
    })}>
      {icon}{children}
    </span>
  )
}

export default MenuItem
