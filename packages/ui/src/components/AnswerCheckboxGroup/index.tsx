import React, { Children, cloneElement, useEffect, useState } from 'react'
import Checkbox from './Checkbox'
import { Transition } from '@headlessui/react'

type PropTypes = {
  selected: Array<string | number>
  onChange?: (value: Array<string | number>) => void
  error?: string
  children: React.ReactNode
}

const AnswerCheckboxGroup = ({ selected, onChange, error, children }: PropTypes) => {
  const [internalSelected, setInternalSelected] = useState(selected ?? [])

  useEffect(() => {
    setInternalSelected(selected ?? [])
  }, [selected])

  const handleOnChange = (value: string | number, checked: boolean) => {
    let newValues = [...internalSelected]
    if (checked) {
      newValues.push(value)
    } else {
      newValues = newValues.filter((v) => v !== value)
    }

    setInternalSelected(newValues)
    onChange?.(newValues)
  }

  return (
    <div className="flex flex-col gap-[30px]">
      {
        Children.map(children, (child) => {
          if (React.isValidElement(child)) {
            return cloneElement(child, {
              selected: (internalSelected ?? []).includes(child.props.value),
              onChange: (v: boolean) => handleOnChange(child.props.value, v),
            } as Partial<unknown>)
          }
        })
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
        <div className="text-danger text-[0.875rem] -mt-[20px]">
          {error}
        </div>
      </Transition>
    </div>
  )
}

AnswerCheckboxGroup.Checkbox = Checkbox

export default AnswerCheckboxGroup
