type Proptypes = {
  className?: string,
  ariaHidden?: boolean
}

const WarningIcon = ({ className = 'h-4 w-4', ariaHidden = true }: Proptypes) => {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className={className} aria-hidden={ariaHidden}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" strokeWidth="2" />
    </svg>
  )
}

export default WarningIcon
