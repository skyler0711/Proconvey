import { forwardRef } from 'react'
import classNames from 'classnames'
import LoadingSpinner from '../LoadingSpinner'

interface PropTypes extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  children: React.ReactNode
  variant?: 'primary' | 'secondary' | 'tertiary' | 'outlined' | 'link' | 'danger' | 'plain'
  size?: 'small' | 'default'
  block?: boolean
  loading?: boolean
  loadingText?: string
  groupFocus?: boolean
  icon?: React.ReactNode
  wrap?: boolean
}

const Button = forwardRef<HTMLButtonElement, PropTypes>(function Button ({
  onClick,
  icon,
  children,
  type = 'button',
  variant = 'primary',
  size = 'default',
  block = false,
  loading = false,
  loadingText,
  disabled = false,
  groupFocus = false,
  className,
  wrap = true,
  ...props
}, ref) {

  const innerClassName = classNames('flex items-center justify-center relative transition focus:outline-none ', {
    'whitespace-nowrap': !wrap,
    'w-full': block,
    // Sizes
    'font-bold text-base px-8 py-4 rounded-lg leading-5': size === 'default' && variant !== 'link',
    'font-medium text-sm px-6 py-[0.7813rem] rounded-lg leading-4': size === 'small' && variant !== 'link',
    'font-medium text-sm p-2 leading-[1.125rem]': variant === 'link',

    // Primary Variant
    'text-white bg-primary hover:bg-primary-hover active:bg-primary-active focus-visible:bg-primary-focus focus-visible:ring-primary-ring focus-visible:ring-2.5': variant === 'primary',
    'group-focus-visible:bg-primary-focus group-focus-visible:ring-primary-ring group-focus-visible:ring-2.5': variant === 'primary' && groupFocus,
    'bg-primary-disabled hover:bg-primary-disabled active:bg-primary-disabled focus-visible:bg-primary-disabled ring-0': variant === 'primary' && disabled,

    // Secondary Variant
    'text-primary bg-secondary hover:bg-secondary-hover active:bg-secondary-active focus-visible:bg-secondary-focus focus-visible:ring-secondary-ring focus-visible:ring-2.5': variant === 'secondary',
    'group-focus-visible:bg-secondary-focus group-focus-visible:ring-secondary-ring group-focus-visible:ring-2.5': variant === 'secondary' && groupFocus,
    'bg-secondary-disabled hover:bg-secondary-disabled active:bg-secondary-disabled focus-visible:bg-secondary-disabled ring-0': variant === 'secondary' && disabled,

    // danger Variant
    'text-white bg-danger hover:bg-opacity-50 active:bg-secondary-active focus-visible:bg-secondary-focus focus-visible:ring-secondary-ring focus-visible:ring-2.5': variant === 'danger',

    // Tertiary Variant
    'shadow-[0_0_0_2px_#62C0C1_inset] text-mint bg-mint bg-opacity-10 hover:bg-opacity-20 active:bg-opacity-20 focus-visible:bg-opacity-20 focus-visible:ring-mint focus-visible:ring-opacity-30 focus-visible:ring-2.5': variant === 'tertiary',

    // Outline Variant
    'shadow-[0_0_0_2px_#674186_inset] text-primary bg-outlined hover:bg-outlined-hover active:bg-outlined-active focus-visible:bg-outlined-focus focus-visible:ring-outlined-ring focus-visible:ring-2.5': variant === 'outlined',
    'group-focus-visible:bg-outlined-focus group-focus-visible:ring-outlined-ring group-focus-visible:ring-2.5': variant === 'outlined' && groupFocus,
    'opacity-20 bg-outlined bg-outlined hover:bg-outlined active:bg-outlined focus-visible:bg-outlined ring-0': variant === 'outlined' && disabled,

    // Link variant
    '-ml-2 -mr-2 text-primary hover:underline active:no-underline focus-visible:bg-secondary rounded-md': variant === 'link',
    'group-focus-visible:bg-secondary': variant === 'link' && groupFocus,
    'text-opacity-20 hover:no-underline focus:bg-transparent': variant === 'link' && disabled,

    // Plain variant
    '!px-2.5 !py-2 focus-visible:ring-primary-ring focus-visible:ring-2': variant === 'plain',
  }, className)

  const loadingClassName = classNames({
    'invisible': loading && !loadingText,
    'visible': !loading && !loadingText,
    'hidden': loading && loadingText,
    'block': !loading && loadingText,
  }, {
    'pl-[0.625rem]': icon,
  }, {
    'whitespace-nowrap': !wrap,
  })


  return (
    <button
      ref={ref}
      className={innerClassName}
      onClick={disabled ? undefined : onClick}
      type={type}
      disabled={disabled || loading}
      {...props}
    >
      {
        loading &&
        <div className={`flex items-center gap-2 ${!loadingText ? 'absolute' : ''}`}>
          {loadingText}
          <LoadingSpinner />
        </div>
      }
      {icon}
      <span className={loadingClassName}>{children}</span>
    </button>
  )
})

export default Button
