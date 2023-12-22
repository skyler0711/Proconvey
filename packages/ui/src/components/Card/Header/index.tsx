type PropTypes = {
  children: React.ReactNode
}

const Header = ({ children }: PropTypes) => {
  return (
    <div className="px-5 py-[1.4375rem]">
      {children}
    </div>
  )
}

export default Header
