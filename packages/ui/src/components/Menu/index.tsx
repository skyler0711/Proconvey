import MenuDropdown from './MenuDropdown'
import MenuItem from './MenuItem'
import MenuSubItem from './MenuSubItem'

type PropTypes = {
  children: React.ReactNode
  icon?: React.ReactNode
}

const Menu = ({ children }: PropTypes) => {

  return (
    <div className="max-w-[294px] h-screen flex flex-col gap-y-[1.875rem] py-[3.75rem] pl-[2.5rem] pr-[0.9375rem]">
      {children}
    </div>
  )
}

Menu.Dropdown = MenuDropdown
Menu.Item = MenuItem
Menu.SubItem = MenuSubItem

export default Menu
