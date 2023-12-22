import { Transition } from '@headlessui/react'
import classNames from 'classnames'
import { forwardRef } from 'react'
import Label from '../Label'

interface PropTypes extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string
  disabled?: boolean
  prefixIcon?: JSX.Element
  error?: string
  help?: string
  SubLabel?: string
}

const Input = forwardRef<HTMLInputElement, PropTypes>(function Input ({
  label,
  disabled = false,
  prefixIcon,
  className,
  error,
  help,
  SubLabel,
  ...props
}, ref) {
  return (
    <div className={classNames('flex-1', { 'opacity-50': disabled }, className)}>
      {label && (
        <Label>
          {label}{' '}
          {SubLabel && (
            <span
              className="text-base opacity-50"
              style={{ color: '#3D403D99' }}>
              (Optional)
            </span>
          )}
        </Label>
      )}
      <div className="relative">
        <input
          ref={ref}
          className={classNames(
            'transition text-base text-body placeholder-input-placeholder w-full px-3 py-[14px] leading-[1.375rem] rounded-lg border focus:border-input-active focus:outline-none focus-visible:ring-2.5 focus-visible:ring-input-ring  disabled:bg-white',
            {
              'pl-[2.875rem]': prefixIcon,

              'border-danger': error,
              'border-input': !error,
            },
          )}
          disabled={disabled}
          {...props}
        />

        {
          prefixIcon &&
          <div className="absolute inset-y-0 flex items-center left-4">
            {prefixIcon}
          </div>
        }

        {
          help && !error &&
          <div className="text-[0.875rem] text-body/60">
            {help}
          </div>
        }

        <Transition
          show={Boolean(error)}
          enter="transition"
          enterFrom="opacity-0 -translate-y-1"
          enterTo="opacity-100 translate-y-0"
          leave="transition"
          leaveFrom="opacity-100 translate-0"
          leaveTo="opacity-0 -translate-y-1"
        >
          <div className="text-danger text-[0.875rem]">
            {error}
          </div>
        </Transition>

        {
          help && error &&
          <div className="text-[0.875rem]">&nbsp;</div>
        }
      </div>
    </div>
  )
})

export default Input
