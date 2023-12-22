import classNames from 'classnames'

interface PropTypes {
  prefix?: string
  value: string | number
  selected?: boolean
  onChange?: (checked: boolean) => void
  error?: string
  children: React.ReactNode
}

const Checkbox = ({
  value,
  prefix,
  selected,
  onChange = () => {},
  children,
  error,
  ...props
}: PropTypes) => {
  return (
    <label className={classNames('flex items-center cursor-pointer min-w-[260px] gap-3.5 bg-white border-2 rounded-lg py-[1.875rem] px-5 text-xl text-body leading-[1.4375rem] font-medium focus-within:ring-offset-0 focus-within:ring-secondary-ring focus-within:ring-2.5', {
      'border-opacity-[15%]': !selected && !error,
      'border-opacity-100': selected && !error,
      'border-primary': !error,
      'border-danger border-opacity-30': error,
    })}>
      <input
        type="checkbox"
        value={value}
        checked={selected}
        onChange={(e) => onChange(e.target.checked)}
        className="border-gainsboro checked:text-mint border-2 rounded w-[1.875rem] h-[1.875rem] focus:ring-0"
        {...props}
      />
      <div className="flex flex-col gap-1.5">
        {
          prefix &&
          <p className="font-medium text-base text-body text-opacity-[60%] leading-[1.125rem]">{prefix}</p>
        }
        {children}
      </div>
    </label>
  )
}

export default Checkbox
