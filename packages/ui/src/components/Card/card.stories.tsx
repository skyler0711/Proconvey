import { ComponentStory, ComponentMeta } from '@storybook/react'
import Card from './index'
import { H3 } from '../Headers'
import Table from '../Table'

export default {
  title: 'Components/Card',
  component: Card,
} as ComponentMeta<typeof Card>

export const ExampleCard: ComponentStory<typeof Card> = () => {
  return (
    <Card>
      <Card.Header>
        <H3>Team Members</H3>
      </Card.Header>
      <Card.Body padContent={false}>
        <Table>
          <Table.Body>
            <Table.Row>
              <Table.Cell>Conveyancer Plan - Sep 2022</Table.Cell>
              <Table.Cell>£250</Table.Cell>
              <Table.Cell>5</Table.Cell>
              <Table.Cell>22 Sepetember 2022</Table.Cell>
            </Table.Row>
            <Table.Row>
              <Table.Cell>Conveyancer Plan - Sep 2022</Table.Cell>
              <Table.Cell>£250</Table.Cell>
              <Table.Cell>5</Table.Cell>
              <Table.Cell>22 Sepetember 2022</Table.Cell>
            </Table.Row>
            <Table.Row>
              <Table.Cell>Conveyancer Plan - Sep 2022</Table.Cell>
              <Table.Cell>£250</Table.Cell>
              <Table.Cell>5</Table.Cell>
              <Table.Cell>22 Sepetember 2022</Table.Cell>
            </Table.Row>
          </Table.Body>
        </Table>
      </Card.Body>
    </Card>
  )
}
