type PropTypes = {
  children: React.ReactNode
}

const Head = ({ children }: PropTypes) => {
  return (
    <thead className="bg-selago">
      {children}
    </thead>
  )
}

export default Head
