import { Transition } from '@headlessui/react'
import React, { useState } from 'react'

type PropTypes = {
  children: (data: Props) => React.ReactNode;
  error?: string
}

type Props = {
  selected: string[]
  onChange: (item: string) => void
}

const Group = ({ children, error }: PropTypes) => {
  const [items, setItems] = useState<string[]>([])

  const toggleItem = (item: string) => {
    if (items.includes(item)) {
      setItems(items.filter(i => i !== item))
    } else {
      setItems([...items, item])
    }
  }

  return <>
    {children({
      selected: items,
      onChange: toggleItem,
    } as Props)}
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
  </>
}

export default Group
