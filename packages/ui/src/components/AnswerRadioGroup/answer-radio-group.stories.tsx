import { useState } from 'react'
import { ComponentStory, ComponentMeta } from '@storybook/react'
import AnswerRadioGroup from './index'

export default {
  title: 'Components/AnswerRadioGroup',
  component: AnswerRadioGroup,
} as ComponentMeta<typeof AnswerRadioGroup>

export const RadioOptions: ComponentStory<typeof AnswerRadioGroup> = () => {
  const [selected, setSelected] = useState<string | number | undefined>('no')

  return (
    <AnswerRadioGroup name="question_1" selected={selected} onChange={(value) => setSelected(value)}>
      <AnswerRadioGroup.Radio value="yes">Yes</AnswerRadioGroup.Radio>
      <AnswerRadioGroup.Radio value="no">No</AnswerRadioGroup.Radio>
    </AnswerRadioGroup>
  )
}

export const ErrorRadioOptions: ComponentStory<typeof AnswerRadioGroup> = () => {
  const [selected, setSelected] = useState<string | number | undefined>('no')

  return (
    <AnswerRadioGroup name="question_1" selected={selected} onChange={(value) => setSelected(value)} error="This is an error">
      <AnswerRadioGroup.Radio value="yes">Yes</AnswerRadioGroup.Radio>
      <AnswerRadioGroup.Radio value="no" error="This is an error">No</AnswerRadioGroup.Radio>
    </AnswerRadioGroup>
  )
}

export const ErrorGroupRadioOptions: ComponentStory<typeof AnswerRadioGroup> = () => {
  const [selected, setSelected] = useState<string | number | undefined>(undefined)

  return (
    <AnswerRadioGroup name="question_1" selected={selected} onChange={(value) => setSelected(value)} error="This is an error">
      <AnswerRadioGroup.Radio value="yes" error="This is an error">Yes</AnswerRadioGroup.Radio>
      <AnswerRadioGroup.Radio value="no" error="This is an error">No</AnswerRadioGroup.Radio>
    </AnswerRadioGroup>
  )
}
