import classNames from 'classnames'
import LoadingSpinner from '../LoadingSpinner'

interface PropTypes extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  icon: React.ReactNode
  loading?: boolean
  disabled?: boolean
  size?: 'default' | 'small'
}

const IconButton = ({
  icon,
  loading = false,
  disabled = false,
  size = 'default',
  ...props
}: PropTypes) => {
  const innerClassName = classNames('transition text-primary flex items-center justify-center rounded-[0.625rem]', {
    'bg-secondary hover:bg-secondary-hover active:bg-secondary-active focus-visible:outline-none focus:outline-none focus-visible:bg-secondary-focus focus-visible:ring-secondary-ring focus-visible:ring-2.5': !disabled,
    'text-opacity-50 bg-secondary-disabled hover:bg-secondary-disabled active:bg-secondary-disabled focus-visible:bg-secondary-disabled ring-0': disabled,
    'w-[3.125rem] h-[3.125rem]': size === 'default',
    'w-[2.5625rem] h-[2.5625rem]': size === 'small',
  })

  return (
    <button
      className={innerClassName}
      {...props}
    >
      {
        loading
          ? <LoadingSpinner />
          : icon
      }
    </button>
  )
}

export default IconButton
