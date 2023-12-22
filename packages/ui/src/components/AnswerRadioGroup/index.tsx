import React, { Children, cloneElement } from 'react'
import { RadioGroup, Transition } from '@headlessui/react'
import Radio from './Radio'
import classNames from 'classnames'

type PropTypes = {
  selected?: string | number | boolean | undefined
  onChange?: (value: string | number | boolean) => void
  children: React.ReactNode
  wrap?: boolean
  error?: string
}

const AnswerRadioGroup = ({ selected, onChange, children, wrap = true, error }: PropTypes) => {
  return (
    <div>
      <RadioGroup
        className={classNames(
          'transition-all flex gap-[1.875rem]',
          {
            'flex-wrap': wrap,
          },
        )}
        value={selected}
        onChange={onChange}
      >
        {
          Children.map(children, (child) => {
            if (React.isValidElement(child)) {
              return cloneElement(child, {
              } as Partial<unknown>)
            }
          })
        }
      </RadioGroup>

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
    </div>
  )
}

AnswerRadioGroup.Radio = Radio

export default AnswerRadioGroup
