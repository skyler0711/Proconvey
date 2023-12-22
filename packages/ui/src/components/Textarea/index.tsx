import { Transition } from '@headlessui/react'
import classNames from 'classnames'
import Label from '../Form/Label'
import { forwardRef } from 'react'

interface PropTypes extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
  label?: string
  disabled?: boolean
  placeholder?: string
  error?: string
  help?: string
}

const Textarea = forwardRef<HTMLTextAreaElement, PropTypes>(function Textarea ({
  label,
  disabled = false,
  className,
  placeholder,
  error,
  help,
  ...props
}, ref) {
  return (
    <div className={classNames('flex-1', {
      'opacity-50': disabled,
    }, className)}>
      {
        label &&
        <Label>{label}</Label>
      }
      <div className="relative">
        <textarea
          ref={ref}
          disabled={disabled}
          {...props}
          placeholder={placeholder}
          className={classNames(
            'transition h-[150px] text-base text-body placeholder-input-placeholder w-full px-3 py-[14px] leading-[1.375rem] rounded-lg border focus:border-input-active focus:outline-none focus-visible:ring-2.5 focus-visible:ring-input-ring disabled:bg-white',
            className,
            {
              'border-danger': error,
              'border-input': !error,
            },
          )}
        />
      </div>

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
        <div className="text-danger text-[0.875rem] absolute">
          {error}
        </div>
      </Transition>

      {
        help && error &&
        <div className="text-[0.875rem]">&nbsp;</div>
      }
    </div>
  )
})

export { Textarea }
