type Proptypes = {
  className?: string,
  ariaHidden?: boolean
}

const UserIcon = ({ className = 'h-6 w-6', ariaHidden = true }: Proptypes) => {
  return (
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" className={className} aria-hidden={ariaHidden}>
      <path fillRule="evenodd" clipRule="evenodd" d="M2 3C2 2.44772 2.44772 2 3 2H17C17.5523 2 18 2.44772 18 3V17C18 17.5523 17.5523 18 17 18L16 18C16 15.2386 13.7614 13 11 13H9C6.23858 13 4 15.2386 4 18L3 18C2.44772 18 2 17.5523 2 17V3ZM6 18H14C14 16.3431 12.6569 15 11 15H9C7.34315 15 6 16.3431 6 18ZM3 0C1.34315 0 0 1.34315 0 3V17C0 18.6569 1.34315 20 3 20H17C18.6569 20 20 18.6569 20 17V3C20 1.34315 18.6569 0 17 0H3ZM8 8C8 9.10457 8.89543 10 10 10C11.1046 10 12 9.10457 12 8C12 6.89543 11.1046 6 10 6C8.89543 6 8 6.89543 8 8ZM10 4C7.79086 4 6 5.79086 6 8C6 10.2091 7.79086 12 10 12C12.2091 12 14 10.2091 14 8C14 5.79086 12.2091 4 10 4Z" fill="currentColor" />
    </svg>
  )
}

export default UserIcon
