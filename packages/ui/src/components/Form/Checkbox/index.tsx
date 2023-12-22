import { Transition } from '@headlessui/react'
import classNames from 'classnames'
import Group from './Group'

interface PropTypes {
  name?: string
  value: string
  selected: string[]
  size?: 'small' | 'base'
  rounded?: boolean
  onChange: (value: string) => void
  children?: React.ReactNode
  error?: string
}

const Checkbox = ({
  name,
  value,
  selected,
  onChange,
  size = 'base',
  rounded,
  error,
  children,
  ...props
}: PropTypes) => {

  return (
    <div className="flex justify-center">
      <label className={classNames('flex items-center cursor-pointer', {
        'text-xl leading-[1.4375rem] font-medium gap-3.5': size === 'base',
        'text-base leading-[1.1875rem] font-normal': size === 'small',

        'text-body': !error,
        'text-danger': error,
      })}>
        <input
          type="checkbox"
          name={name}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          checked={selected.includes(value)}
          className={classNames('border-2 focus:ring-0 focus:ring-transparent', {
            'rounded-full': rounded,
            'rounded': !rounded,

            'w-[1.875rem] h-[1.875rem] checked:text-mint focus-visible:ring-secondary-ring focus-visible:ring-offset-0 focus-visible:ring-2.5': size === 'base',
            'w-[1.5rem] h-[1.5rem] checked:text-primary focus-visible:ring-primary-ring focus-visible:ring-offset-0 focus-visible:ring-2.5': size === 'small',

            'border-gainsboro': !error,
            '!border-danger': error,
          })}
          {...props}
        />
        {children}
      </label>

      <Transition
        show={Boolean(error)}
        enter="transition"
        enterFrom="opacity-0 -translate-y-1"
        enterTo="opacity-100 translate-y-0"
        leave="transition"
        leaveFrom="opacity-100 translate-0"
        leaveTo="opacity-0 -translate-y-1"
      >
        <div className="text-danger text-[0.875rem] mt-[2px]">
          {error}
        </div>
      </Transition>
    </div>
  )
}

Checkbox.Group = Group

export default Checkbox
