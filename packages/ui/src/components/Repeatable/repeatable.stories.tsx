import { ComponentStory, ComponentMeta } from '@storybook/react'
import { H3 } from '../Headers'
import Repeatable from './index'

export default {
  title: 'Components/Repeatable',
  component: Repeatable,
} as ComponentMeta<typeof Repeatable>

export const BasicRepeatable: ComponentStory<typeof Repeatable> = () => {
  return (
    <Repeatable>
      <Repeatable.Tabs>
        <Repeatable.Tab>Mortgage, charge or loan 1</Repeatable.Tab>
        <Repeatable.Tab>Mortgage, charge or loan 1</Repeatable.Tab>
        <Repeatable.Tab>Mortgage, charge or loan 1</Repeatable.Tab>
      </Repeatable.Tabs>
      <Repeatable.Panels>
        <Repeatable.Panel>
          <H3>Mortgage, charge or loan 1</H3>
        </Repeatable.Panel>
        <Repeatable.Panel>
          <H3>Mortgage, charge or loan 2</H3>
        </Repeatable.Panel>
        <Repeatable.Panel>
          <H3>Mortgage, charge or loan 3</H3>
        </Repeatable.Panel>
      </Repeatable.Panels>
    </Repeatable>
  )
}

export const ErrorRepeatable: ComponentStory<typeof Repeatable> = () => {
  return (
    <Repeatable>
      <Repeatable.Tabs>
        <Repeatable.Tab hasError>Mortgage, charge or loan 1</Repeatable.Tab>
        <Repeatable.Tab>Mortgage, charge or loan 1</Repeatable.Tab>
        <Repeatable.Tab hasError>Mortgage, charge or loan 1</Repeatable.Tab>
      </Repeatable.Tabs>
      <Repeatable.Panels>
        <Repeatable.Panel>
          <H3>Mortgage, charge or loan 1</H3>
        </Repeatable.Panel>
        <Repeatable.Panel>
          <H3>Mortgage, charge or loan 2</H3>
        </Repeatable.Panel>
        <Repeatable.Panel>
          <H3>Mortgage, charge or loan 3</H3>
        </Repeatable.Panel>
      </Repeatable.Panels>
    </Repeatable>
  )
}
