import { ComponentStory, ComponentMeta } from '@storybook/react'
import Checkbox from './index'

export default {
  title: 'Components/Forms/Checkbox',
  component: Checkbox,
} as ComponentMeta<typeof Checkbox>


export const SquareCheckbox: ComponentStory<typeof Checkbox> = () => {
  return (
    <Checkbox.Group>
      {({ selected, onChange }) => (
        <Checkbox
          value="1"
          selected={selected}
          onChange={onChange}
        >
          Example Checkbox
        </Checkbox>
      )}
    </Checkbox.Group>
  )
}

export const SmallCheckbox: ComponentStory<typeof Checkbox> = () => {
  return (
    <Checkbox.Group>
      {({ selected, onChange }) => (
        <Checkbox
          value="1"
          size="small"
          selected={selected}
          onChange={onChange}
        >
          Example Checkbox
        </Checkbox>
      )}
    </Checkbox.Group>
  )
}

export const RoundedCheckbox: ComponentStory<typeof Checkbox> = () => {
  return (
    <Checkbox.Group>
      {({ selected, onChange }) => (
        <Checkbox
          value="1"
          rounded={true}
          selected={selected}
          onChange={onChange}
        >
          Example Checkbox
        </Checkbox>
      )}
    </Checkbox.Group>
  )
}

export const ErrorCheckbox: ComponentStory<typeof Checkbox> = () => {
  return (
    <Checkbox.Group>
      {({ selected, onChange }) => (
        <Checkbox
          value="1"
          selected={selected}
          onChange={onChange}
          error="This is an error"
        >
          Example Checkbox
        </Checkbox>
      )}
    </Checkbox.Group>
  )
}
