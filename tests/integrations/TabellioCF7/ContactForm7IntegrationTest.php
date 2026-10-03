<?php

declare(strict_types=1);

namespace IntegrationTests\TabellioCF7;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tabellio_CF7\Item;
use Tabellio_CF7\Option;
use Tabellio_CF7\Submission;
use WPCF7_ContactForm;
use WPCF7_Submission;

defined('WPCF7_VERSION') || define('WPCF7_VERSION', '6.1');

class ContactForm7IntegrationTest extends TestCase
{
    #[Test]
    #[Group('integration')]
    public function shouldStoreSubmissionAsCustomPostType()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Test Form', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello world',
            'phone' => '081234567890',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $this->assertSubmissionPostStatus('publish', $form_id);
    }

    #[Test]
    #[Group('integration')]
    public function shouldNotStoreSubmissionWhenRecordingDisabled()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('No Record Form', []);

        $this->triggerSubmission($contact_form, [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'This should not be stored',
        ]);

        $this->assertSubmissionCount(0, $form_id);
    }

    #[Test]
    #[Group('integration')]
    public function shouldUseSubjectFieldValueAsPostTitle()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Custom Subject', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'message' => 'Test message',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $this->assertSame('Alice Smith', $this->latestSubmission($form_id)->post_title);
    }

    #[Test]
    #[Group('integration')]
    public function shouldUseFallbackTitleWhenSubjectFieldIsEmpty()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Fallback Title', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => '',
            'email' => 'no-name@example.com',
            'message' => 'No name provided',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $this->assertSame(
            'Submission for "Fallback Title"',
            $this->latestSubmission($form_id)->post_title
        );
    }

    #[Test]
    #[Group('integration')]
    public function shouldStoreFormDataAsPostMeta()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Meta Test', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Meta Test',
            'email' => 'meta@example.com',
            'phone' => '081234567890',
            'message' => 'Check meta',
            'interest' => 'wordpress',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $submission_id = $this->latestSubmission($form_id)->ID;

        $this->assertSame('Meta Test', \get_post_meta($submission_id, 'name', true));
        $this->assertSame('meta@example.com', \get_post_meta($submission_id, 'email', true));
        $this->assertSame('081234567890', \get_post_meta($submission_id, 'phone', true));
        $this->assertSame('Check meta', \get_post_meta($submission_id, 'message', true));
        $this->assertSame('wordpress', \get_post_meta($submission_id, 'interest', true));
    }

    #[Test]
    #[Group('integration')]
    public function shouldUseMessageFieldAsPostExcerpt()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Excerpt Test', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Excerpt User',
            'message' => 'This is the excerpt content',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $this->assertSame(
            'This is the excerpt content',
            $this->latestSubmission($form_id)->post_excerpt
        );
    }

    #[Test]
    #[Group('integration')]
    public function shouldHaveInitialUnreadStatus()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Read Status', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Read Status',
            'message' => 'Check status',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $item = new Item($this->latestSubmission($form_id)->ID);
        $this->assertTrue($item->is_unread());
        $this->assertFalse($item->is_read());
    }

    #[Test]
    #[Group('integration')]
    public function shouldMarkReadStatusCorrectly()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Mark Read', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Mark Read Test',
            'message' => 'Check mark read',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $item = new Item($this->latestSubmission($form_id)->ID);

        $this->assertTrue($item->mark_read());
        $this->assertTrue($item->is_read());
        $this->assertFalse($item->is_unread());

        $this->assertTrue($item->mark_unread());
        $this->assertTrue($item->is_unread());
        $this->assertFalse($item->is_read());
    }

    #[Test]
    #[Group('integration')]
    public function shouldStoreSubmissionAuthorAsSubscriber()
    {
        [$form_id, $contact_form] = $this->createConfiguredForm('Author Store', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
            Option::STORE_AUTHOR_KEY => 'on',
            Option::NAME_FIELD_KEY => 'name',
            Option::EMAIL_FIELD_KEY => 'email',
            Option::PHONE_FIELD_KEY => 'phone',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Store Author',
            'email' => 'store.author@testing.test',
            'phone' => '089876543210',
            'message' => 'Check author storage',
        ]);

        $this->assertSubmissionCount(1, $form_id);
        $item = new Item($this->latestSubmission($form_id)->ID);

        $this->assertNotNull($item->author());
        $this->assertSame('store.author@testing.test', $item->author_email);
        $this->assertSame('089876543210', $item->author_phone);
    }

    #[Test]
    #[Group('integration')]
    public function shouldRegisterCustomPostType()
    {
        $post_type = \get_post_type_object(Submission::POST_TYPE);

        $this->assertNotNull($post_type);
        $this->assertSame('tabellio-submission', $post_type->name);
        $this->assertFalse($post_type->public);
    }

    #[Test]
    #[Group('integration')]
    public function shouldAddTabellioPanelToEditorPanels()
    {
        [$form_id, $contact_form] = $this->createContactForm('Editor Panel Test');

        $panels = \apply_filters('wpcf7_editor_panels', [], $contact_form);

        $this->assertArrayHasKey(Submission::MENU_SLUG, $panels);
        $this->assertArrayHasKey('title', $panels[Submission::MENU_SLUG]);
        $this->assertArrayHasKey('callback', $panels[Submission::MENU_SLUG]);
    }

    #[Test]
    #[Group('integration')]
    public function shouldFireBeforeAndAfterSaveActions()
    {
        $before_save_called = false;
        $after_save_called = false;

        \add_action('tabellio_before_save', function () use (&$before_save_called) {
            $before_save_called = true;
        });

        \add_action('tabellio_after_save', function () use (&$after_save_called) {
            $after_save_called = true;
        });

        [$form_id, $contact_form] = $this->createConfiguredForm('Hook Test', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, [
            'name' => 'Hook Test',
            'message' => 'Check hooks',
        ]);

        $this->assertTrue($before_save_called, 'tabellio_before_save should have been triggered');
        $this->assertTrue($after_save_called, 'tabellio_after_save should have been triggered');
    }

    #[Test]
    #[Group('integration')]
    public function shouldStoreSubmissionLinkedToCorrectForm()
    {
        [$form_id_a, $contact_form_a] = $this->createConfiguredForm('Form A', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        [$form_id_b, $contact_form_b] = $this->createConfiguredForm('Form B', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form_a, ['name' => 'From A', 'message' => 'A']);
        $this->triggerSubmission($contact_form_b, ['name' => 'From B', 'message' => 'B']);

        $this->assertSubmissionCount(1, $form_id_a);
        $this->assertSubmissionCount(1, $form_id_b);
        $this->assertSame('From A', $this->latestSubmission($form_id_a)->post_title);
        $this->assertSame('From B', $this->latestSubmission($form_id_b)->post_title);
    }

    #[Test]
    #[Group('integration')]
    public function shouldHandleMultipleSubmissionsForSameForm()
    {
        [, $contact_form] = $this->createConfiguredForm('Multi Submit', [
            Option::SHOULD_RECORD_KEY => 'on',
            Option::SUBJECT_FIELD_KEY => 'name',
            Option::MESSAGE_FIELD_KEY => 'message',
        ]);

        $this->triggerSubmission($contact_form, ['name' => 'First', 'message' => 'Msg 1']);
        $this->triggerSubmission($contact_form, ['name' => 'Second', 'message' => 'Msg 2']);
        $this->triggerSubmission($contact_form, ['name' => 'Third', 'message' => 'Msg 3']);

        $this->assertSubmissionCount(3, $this->formIdFromContactForm($contact_form));
    }

    private function createContactForm(string $title): array
    {
        $post_id = \wp_insert_post([
            'post_type' => WPCF7_ContactForm::post_type,
            'post_status' => 'publish',
            'post_title' => $title,
            'post_content' => '[text name][email email][textarea message]',
        ]);

        self::assertNotWPError($post_id);

        $contact_form = WPCF7_ContactForm::get_instance((int) $post_id);

        self::assertInstanceOf(WPCF7_ContactForm::class, $contact_form);

        return [$post_id, $contact_form];
    }

    private function createConfiguredForm(string $title, array $properties): array
    {
        [$post_id, $contact_form] = $this->createContactForm($title);

        if (!empty($properties)) {
            $contact_form->set_properties([Option::FORM_PROP_KEY => $properties]);
            $contact_form->save();
        }

        return [$post_id, $contact_form];
    }

    private function triggerSubmission(WPCF7_ContactForm $contact_form, array $form_data = []): void
    {
        global $wpcf7;

        $_POST = array_merge($_POST, $form_data);

        $option = Option::get($contact_form);
        \error_log('Tabellio CF7 Integration Test - should_record: ' . ($option ? ($option->should_record ? 'true' : 'false') : 'no option'));

        $wpcf7 = (object) [
            'contact_form' => $contact_form,
            'submission' => WPCF7_Submission::get_instance($contact_form, ['skip_mail' => true]),
        ];

        \do_action('wpcf7_before_send_mail', $contact_form);
    }

    private function formIdFromContactForm(WPCF7_ContactForm $cf7): int
    {
        return (int) $cf7->id();
    }

    private function latestSubmission(int $form_id): \WP_Post
    {
        $submissions = \get_posts([
            'post_type' => Submission::POST_TYPE,
            'post_parent' => $form_id,
            'posts_per_page' => 1,
            'orderby' => 'ID',
            'order' => 'DESC',
        ]);

        self::assertNotEmpty(
            $submissions,
            'Expected at least one submission for form ' . $form_id
        );

        return $submissions[0];
    }

    private function assertSubmissionCount(int $expected, int $form_id): void
    {
        $actual = \count(
            \get_posts([
                'post_type' => Submission::POST_TYPE,
                'post_parent' => $form_id,
            ])
        );

        self::assertSame(
            $expected,
            $actual,
            sprintf(
                'Expected %d submission(s) for form %d, got %d.',
                $expected,
                $form_id,
                $actual
            )
        );
    }

    private function assertSubmissionPostStatus(string $expected_status, int $form_id): void
    {
        $posts = \get_posts([
            'post_type' => Submission::POST_TYPE,
            'post_parent' => $form_id,
            'posts_per_page' => 1,
        ]);

        self::assertNotEmpty(
            $posts,
            'Expected submission for form ' . $form_id
        );

        self::assertSame(
            $expected_status,
            $posts[0]->post_status,
            sprintf(
                'Expected post_status "%s", got "%s".',
                $expected_status,
                $posts[0]->post_status
            )
        );
    }
}
