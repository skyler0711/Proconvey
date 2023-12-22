type Proptypes = {
  className?: string,
  ariaHidden?: boolean
}

const ChevronDownIcon = ({ className = 'h-6 w-6', ariaHidden = true }: Proptypes) => {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className={className} aria-hidden={ariaHidden}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
    </svg>
  )
}

export default ChevronDownIcon
