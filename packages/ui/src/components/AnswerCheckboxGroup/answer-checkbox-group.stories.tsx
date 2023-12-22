import { useState } from 'react'
import { ComponentStory, ComponentMeta } from '@storybook/react'
import AnswerCheckboxGroup from './index'

export default {
  title: 'Components/AnswerCheckboxGroup',
  component: AnswerCheckboxGroup,
} as ComponentMeta<typeof AnswerCheckboxGroup>

export const CheckboxOptions: ComponentStory<typeof AnswerCheckboxGroup> = () => {
  const [selected, setSelected] = useState<Array<string | number>>(['Wade Warren'])

  return (
    <AnswerCheckboxGroup selected={selected} onChange={(value) => setSelected(value)}>
      <AnswerCheckboxGroup.Checkbox prefix="Attorney 1" value="Alex Smith">Alex Smith</AnswerCheckboxGroup.Checkbox>
      <AnswerCheckboxGroup.Checkbox prefix="Attorney 2" value="Wade Warren">Wade Warren</AnswerCheckboxGroup.Checkbox>
    </AnswerCheckboxGroup>
  )
}
