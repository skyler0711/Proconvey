import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  active?: boolean
  icon?: React.ReactNode
}

const MenuSubItem = ({ children, active = false, icon }: PropTypes) => {
  return (
    <span className={classNames('flex items-center gap-6 text-base leading-[1.1875rem] font-medium py-2 px-4 focus:outline-none focus-visible:bg-primary/10 focus-visible:text-primary rounded-[0.625rem]', {
      'bg-primary bg-opacity-10 text-primary rounded-[0.625rem]': active,
    })}>
      {icon}{children}
    </span>
  )
}

export default MenuSubItem
